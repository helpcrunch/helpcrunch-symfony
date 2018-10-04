Helpcrunch Symfony Bundle

This bundle contains common classes like basic Symfony 
controller, entity and repository for using them in another Symfony projects.
To use it do next:
1) Add next lines into `composer.json`: 
`"repositories" : [
    {
     "type": "vcs",
     "url": "https://github.com/helpcrunch/helpcrunch-symfony.git"
    }
],`
2) Define Helpcrunch namespace under "autoload" section in composer.json: `"Helpcrunch\\": "vendor/helpcrunch/helpcrunch-symfony/src/"`
2) Run `composer require helpcrunch/helpcrunch-symfony:"master:dev"`