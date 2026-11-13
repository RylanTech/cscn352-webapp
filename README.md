# Pizza configurator

## Setup

1. Create the tables in the `pizza` database:

   ```sh
   mysql -h localhost -u pizza -p pizza < schema.sql
   ```

2. Set `PIZZA_DB_PASSWORD` to the database user's password in the web server
   environment. `PIZZA_DB_HOST`, `PIZZA_DB_NAME`, and `PIZZA_DB_USER` can also
   be set if the defaults (`localhost`, `pizza`, and `pizza`) do not apply.

3. Ensure PHP has the `pdo_mysql` extension enabled, then serve this directory
   with PHP and open `login.php`. New users can create an account from the login
   page.

Orders save the selected pizzas and delivery address. Payment card details are
not collected or stored.
