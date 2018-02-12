<?php
use Symfony\Component\Yaml\Yaml;

/** @var $container \Symfony\Component\DependencyInjection\ContainerBuilder */
$container;

$params = [
    'database_host' => getenv('SYMFONY__CLEANSTREET__DATABASE_HOST'),
    'database_port' => getenv('SYMFONY__CLEANSTREET__DATABASE_PORT'),
    'database_name' => getenv('SYMFONY__CLEANSTREET__DATABASE_NAME'),
    'database_user' => getenv('SYMFONY__CLEANSTREET__DATABASE_USER'),
    'database_password' => getenv('SYMFONY__CLEANSTREET__DATABASE_PASSWORD'),
    'mailer_transport' => getenv('SYMFONY__CLEANSTREET__MAILER_TRANSPORT'),
    'mailer_host' => getenv('SYMFONY__CLEANSTREET__MAILER_HOST'),
    'mailer_user' => getenv('SYMFONY__CLEANSTREET__MAILER_USER'),
    'mailer_password' => getenv('SYMFONY__CLEANSTREET__MAILER_PASSWORD'),
    'mailer_encryption' => getenv('SYMFONY__CLEANSTREET__MAILER_ENCRYPTION'),
    'secret' => getenv('SYMFONY__CLEANSTREET__SECRET'),
    'app_messagebird_key' => getenv('SYMFONY__CLEANSTREET__APP_MESSAGEBIRD_KEY'),
    'piwik_site_id' => getenv('SYMFONY__CLEANSTREET__PIWIK_SITE_ID'),
    'app_phone_enabled' => getenv('SYMFONY__CLEANSTREET__APP_PHONE_ENABLED')
];

foreach ($params as $key => $value) {
    $container->setParameter($key, $value);
}
