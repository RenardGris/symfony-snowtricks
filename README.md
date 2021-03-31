# Symfony snowtricks

## Presentation


this project was made during my apprenticeship in programming
It's a blog about snowboard for people who want to learn it and help others.
I have to use symfony framework to do it.

### What's in the blog ?
Home page with all tricks paginate by 15.\
Show selected trick with medias and comments.\
User registration (send email for validation, so validate account)\
Request and reset password.\
Only auth user can comment, update or delete tricks


## Installation

1.  get code from the repository
```
git clone https://github.com/RenardGris/symfony-snowtricks.git
```

2.  install dependency
```
composer install
```

3.  in .env, define DATABASE_URL according to yours, and also MAILER_URL to use swiftmailer

``` 
# DATABASE_URL="sqlite:///%kernel.project_dir%/var/data.db"
# DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/db_name?serverVersion=mariadb-10.4.10"
# DATABASE_URL="postgresql://db_user:db_password@127.0.0.1:5432/db_name?serverVersion=13&charset=utf8"
```
``` 
# MAILER_URL=gmail://mail:password@localhost
```

4.  else you can make tables and data with the next symfony cli
``` 
 php bin/console doctrine:migrations:migrate
```
``` 
 php bin/console doctrine:fixtures:load
```