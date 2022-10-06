# Leonardo
Make new pizza recipes.
Choose your ingredients, and the total price of your pizza is automatically calculated.


# Setup
```
git clone <this repo>
cd <this repo>/
composer install
```

Create a env.local file with DATABASE_URL=[the connection parameters to your database]
(See .env file for more informations)

If needed, you can create a database with :
```
php bin/console doctrine:database:create
```

Run migration to create all needed tables :
```
php bin/console doctrine:migrations:migrate
```

Insert some fake datas :
```
php bin/console doctrine:fixtures:load
```


# Run
Now start your symfony server with :
```
symfony server:start
```

Et voilà :
http://127.0.0.1:8000/


# TODO
develop other pages for editing and adding new ingredients and pizza
