<?php
/*
 * Local configuration file to provide any overrides to your app.php configuration.
 * Copy and save this file as app_local.php and make changes as required.
 * Note: It is not recommended to commit files with credentials such as app_local.php
 * into source code version control.
 */
return [
    /*
     * Debug Level:
     *
     * Production Mode:
     * false: No error messages, errors, or warnings shown.
     *
     * Development Mode:
     * true: Errors and warnings shown.
     */
    'debug' => filter_var(env('DEBUG', true), FILTER_VALIDATE_BOOLEAN),

    /*
     * Security and encryption configuration
     *
     * - salt - A random string used in security hashing methods.
     *   The salt value is also used as the encryption key.
     *   You should treat it as extremely sensitive data.
     */
    'Security' => [
        'salt' => env('SECURITY_SALT', '7474d54405757a30baea352618f213a1510088968e9d5f474393df73489b5f74'),
    ],

    /*
     * Connection information used by the ORM to connect
     * to your application's datastores.
     *
     * See app.php for more configuration options.
     */
    'Datasources' => [
        'default' => [
            'host' => 'localhost',
            /*
             * CakePHP will use the default DB port based on the driver selected
             * MySQL on MAMP uses port 8889, MAMP users will want to uncomment
             * the following line and set the port accordingly
             */
            //'port' => 'non_standard_port_number',

            'username' => 'root',
            'password' => '',

            'database' => 'sfa',
            /*
             * If not using the default 'public' schema with the PostgreSQL driver
             * set it here.
             */
            //'schema' => 'myapp',

            /*
             * You can use a DSN string to set the entire configuration
             */
            'url' => env('DATABASE_URL', null),
        ],

        /*
         * The test connection is used during the test suite.
         */
        'test' => [
            'host' => 'localhost',
            //'port' => 'non_standard_port_number',
            'username' => 'my_app',
            'password' => 'secret',
            'database' => 'test_myapp',
            //'schema' => 'myapp',
            'url' => env('DATABASE_TEST_URL', 'sqlite://127.0.0.1/tmp/tests.sqlite'),
        ],
    ],

    /*
     * Email configuration.
     *
     * Host and credential configuration in case you are using SmtpTransport
     *
     * See app.php for more configuration options.
     */
    'EmailTransport' => [
        'default' => [
            'host' => 'localhost',
            'port' => 25,
            'username' => null,
            'password' => null,
            'client' => null,
            'url' => env('EMAIL_TRANSPORT_DEFAULT_URL', null),
        ],
    ],

    /*
     * POP3 settings for client proposals mailbox import.
     */
    'Pop3ClientProposal' => [
        'host' => env('POP3_CLIENT_PROPOSAL_HOST', 'dc14.etius.jp'),
        'port' => (int)env('POP3_CLIENT_PROPOSAL_PORT', 995),
        'username' => env('POP3_CLIENT_PROPOSAL_USER', 'sfa-teian@icz.co.jp'),
        'password' => env('POP3_CLIENT_PROPOSAL_PASS', 'vhqrv4Bz'),
        'ssl' => filter_var(env('POP3_CLIENT_PROPOSAL_SSL', true), FILTER_VALIDATE_BOOLEAN),
        'novalidate_cert' => filter_var(env('POP3_CLIENT_PROPOSAL_NOVALIDATE_CERT', true), FILTER_VALIDATE_BOOLEAN),
        'max_messages' => (int)env('POP3_CLIENT_PROPOSAL_MAX_MESSAGES', 100),
    ],

    /*
     * POP3 settings for BP procurement mailbox import.
     * Same mail server as client proposals, separate mailbox user/address.
     */
    'Pop3BpProcurement' => [
        'host' => env('POP3_BP_PROCUREMENT_HOST', 'dc14.etius.jp'),
        'port' => (int)env('POP3_BP_PROCUREMENT_PORT', 995),
        'username' => env('POP3_BP_PROCUREMENT_USER', 'sfa-chotatsu@icz.co.jp'),
        'password' => env('POP3_BP_PROCUREMENT_PASS', 'vhqrv4Bz'),
        'ssl' => filter_var(env('POP3_BP_PROCUREMENT_SSL', true), FILTER_VALIDATE_BOOLEAN),
        'novalidate_cert' => filter_var(env('POP3_BP_PROCUREMENT_NOVALIDATE_CERT', true), FILTER_VALIDATE_BOOLEAN),
        'max_messages' => (int)env('POP3_BP_PROCUREMENT_MAX_MESSAGES', 100),
    ],
];
