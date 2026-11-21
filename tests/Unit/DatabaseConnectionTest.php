<?php

declare(strict_types=1);

namespace Modules\Setting\Tests\Unit;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Modules\Setting\Models\DatabaseConnection;
use Modules\Setting\Tests\TestCase;
use PDO;

class DatabaseConnectionTest extends TestCase
{
    /**
     * Verifica che il modello DatabaseConnection possa essere istanziato.
     */
    public function test_database_connection_model_can_be_instantiated(): void
    {
        $connection = new DatabaseConnection;

        $this->assertInstanceOf(DatabaseConnection::class, $connection);
    }

    /**
     * Verifica che i casts siano definiti correttamente.
     */
    public function test_casts_are_configured_correctly(): void
    {
        $connection = new DatabaseConnection;
        $casts = $connection->getCasts();

        // Una chiave per asserzione, e non l'array intero: `getCasts()` di
        // Eloquent **aggiunge** la chiave primaria col suo tipo
        // (`'id' => 'int'`), quindi un confronto sull'array completo falliva su
        // una riga che il modello non dichiara e che non ci riguarda. Legare un
        // test agli interni del framework lo fa fallire al prossimo
        // aggiornamento, per qualcosa che non e' nostro
        // (base_quaeris_fila5#241).
        $this->assertSame('integer', $casts['port']);
        $this->assertSame('boolean', $casts['strict']);
        $this->assertSame('array', $casts['options']);
    }

    /**
     * Verifica che il metodo testConnection funzioni correttamente quando la connessione ha successo.
     */
    public function test_test_connection_succeeds(): void
    {
        // ## Perche' qui non si mocka niente
        //
        // Questo test mockava le facade `DB` e `Config`, e non poteva
        // funzionare per due ragioni indipendenti:
        //
        // 1. `Config::shouldReceive('set')` rende la facade un mock **stretto**,
        //    e `testConnection()` oltre a scrivere **legge**
        //    (`config('database.connections')`): la lettura non prevista
        //    falliva con «Received ..._Repository::get(), but no expectations
        //    were specified». Il mock accusava il codice di fare una cosa
        //    legittima.
        // 2. `DB::shouldReceive(...)` sopravvive al corpo del test: il finto
        //    resta installato nel container, e la chiusura di
        //    `DatabaseTransactions`, dentro `parent::tearDown()`, scorre
        //    `DB::getConnections()`. Nessun test prevede quella chiamata, e il
        //    fallimento arrivava **dopo** il test, da XotBaseTestCase.
        //    `Mockery::close()` non basta: verifica le aspettative, non
        //    disinstalla la facade.
        //
        // E soprattutto: mockare `getPdo()` per farlo restituire un PDO finto
        // non verifica **niente** di `testConnection()`, il cui unico lavoro e'
        // aprire una connessione vera e dire se ci riesce. Il database di test
        // c'e' — lo usa questa stessa suite — quindi si prova contro quello
        // (base_quaeris_fila5#241).
        // Accessori tipizzati, non `Config::get()` piu' un cast: `get()`
        // restituisce `mixed`, e `(int) $mixed` e' il genere di cast che
        // zittisce l'analisi invece di dirle qualcosa. `Config::integer()` e
        // `Config::string()` dichiarano cosa ci si aspetta e lanciano se il
        // valore non e' quello.
        $host = Config::string('database.connections.mysql.host');
        // `Config::integer()` qui rifiuta, e ha ragione: `config/database.php`
        // prende la porta da `env('DB_PORT')` e non la converte, quindi il
        // valore e' la **stringa** '3306'. Si legge come stringa e si converte
        // da stringa — che e' una conversione dichiarata, non un cast su
        // `mixed`.
        $porta = (int) Config::string('database.connections.mysql.port');
        $database = Config::string('database.connections.mysql.database');

        $connection = new DatabaseConnection([
            'driver' => 'mysql',
            'host' => $host,
            'port' => $porta,
            'database' => $database,
            'username' => Config::string('database.connections.mysql.username'),
            'password' => Config::string('database.connections.mysql.password'),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => 'InnoDB',
        ]);

        $result = $connection->testConnection();

        $this->assertTrue($result);

        // La connessione di prova e' stata registrata davvero.
        $registrata = Config::get('database.connections.test_connection');
        $this->assertIsArray($registrata);
        $this->assertSame($database, $registrata['database']);
    }

    /**
     * Verifica che il metodo testConnection ritorni false quando la connessione fallisce.
     */
    public function test_test_connection_fails(): void
    {
        // Vedi il test gemello: niente mock. Un host che non si risolve fa
        // fallire la connessione per davvero, che e' esattamente il caso che
        // questo test deve coprire — e lo copre senza simulare l'eccezione che
        // pretende di osservare.
        $connection = new DatabaseConnection([
            'driver' => 'mysql',
            'host' => 'host.che.non.esiste.invalid',
            'port' => 3306,
            'database' => 'test_db',
            'username' => 'user',
            'password' => 'password',
        ]);

        $result = $connection->testConnection();

        $this->assertFalse($result);
    }

    /**
     * Resetta i mock dopo ogni test.
     */
    protected function tearDown(): void
    {
        // Le facade mockate vanno **restituite** prima della chiusura della
        // classe base, non soltanto chiuse.
        //
        // `Mockery::close()` verifica le aspettative e libera Mockery, ma non
        // disinstalla il finto dalla facade: `DB` resta un mock stretto anche
        // dopo. E la chiusura di `DatabaseTransactions`, dentro
        // `parent::tearDown()`, annulla le transazioni scorrendo
        // `DB::getConnections()` — una chiamata che nessun test ha previsto,
        // quindi:
        //
        //     BadMethodCallException: Received
        //     Mockery_..._DatabaseManager::getConnections(), but no
        //     expectations were specified
        //
        // Il fallimento arrivava **dopo** il corpo del test, da
        // XotBaseTestCase.php:230, e accusava il teardown di una cosa che aveva
        // fatto il test. `clearResolvedInstances()` rimette le facade vere in
        // circolazione prima che la classe base le usi
        // (base_quaeris_fila5#241).
        DB::clearResolvedInstances();
        Config::clearResolvedInstances();

        \Mockery::close();
        parent::tearDown();
    }
}
