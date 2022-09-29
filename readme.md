<p align="center"><img src="https://brainstation-23.com/wp-content/uploads/2019/02/BS-Logo-Blue_Hr.svg"></p>

# Approval Management System

## Project Installation Process

### Clone or download from github
```bash
git clone https://gitlab.com/partex-star/ams23.git
```

### Install dependency
- Make sure 'extension=ldap' is enable on php.ini
- From terminal/ssh bash/commandline use below code to install necessary package

```bash
composer install
```

### Create a mysql database and import data
- Create a new database name 'bs23_ams23'
- Import fresh database from database-backup folder 'bs23_ams23.sql'

### Copy .env file from .env.local/.env.server
- For local development copy .env file from .env.local
- For production server copy .env file from .env.server


### Update login authentication
- Edit file 'RootDirectory\app\Http\Controllers\Auth\LoginController.php'
- Comment out the below code of line for production server.
- For local development use this code otherwise comment out for production.

```PHP
$password = Hash::make('123456');
// exit();
if (Auth::attempt(['email' => $mail, 'password' => '123456'])) {
    return redirect()->intended('dashboard');
}
exit();
```


[Visit the URL](http://127.0.0.1:8000)

### Sample credentials for local development
- admin:
    - username: brain23@ams.com
    - password: 123456


### For all production server test crediential go to file.
- '/Root/xtemp/access.txt'
