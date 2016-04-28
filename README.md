# Framework

How to use

Create config file in app/config/config.php

```
<?php

return array(
        'mode'        => 'dev',
        'routes'      => include('routes.php'),
        'main_layout' => __DIR__.'/../../src/Blog/views/layout.html.php',
        'error_500'   => __DIR__.'/../../src/Blog/views/500.html.php',
        'pdo'         => array(
                'dns'      => 'mysql:dbname=name_schema;host=your_host',
                'user'     => 'user',
                'password' => 'password'
        ),
        'security'    => array(
                'user_class'  => 'Blog\\Model\\User',
                'login_route' => '/login'
        )
);
```
