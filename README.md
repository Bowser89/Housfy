# Housify Technical Test

This project contains the technical test or Housify.

Written and directed by Augusto Lamona. 

## Development Flow

All development work occurs on the `develop` branch.
The `master` branch is used to create new releases by merging current head of the `develop` branch.
You should create a feature-branch, branching from `develop`, whenever you need to add some changes to the `master` branch.
If those changes are accepted they will be merged by the repository maintainer.

## Provisioning

To ease local development you have to install these tools:

* [Docker CE](https://www.docker.com/) - At least version 18.06.0
* [Docker-Compose](https://docs.docker.com/compose/)

**Note**: It is recommended to follow [this guide](https://docs.docker.com/install/linux/linux-postinstall/#manage-docker-as-a-non-root-user) in order to manage Docker as a non-root user.

In the project's root folder there is the main file where the architecture is described: `docker-compose.yml`. 
In there you can find all services configured that you'll get. 
In order to have everything work correctly you have to copy the `.env.example` file to the `.env`. 
The latter holds all the environment variables used by docker-compose to run all the containers.

The project images used are: **nginx**, **mysql**, **phpmyadmin**, **redis** and **php-fpm**.

To bring up all the containers, from project's root folder, run:

```
docker-compose up --build
```

**Note**: If you want to run in detached mode just use the `-d` flag.

You can monitor all active containers running:

```
docker-compose ps
```

To stop all the containers just run:

```
docker-compose stop
```

To access the php-fpm, from project root folder just run:

```
docker-compose exec php-fpm sh
```

In order to set up the database environment properly, once the docker environment 
provisioning is completed, from your project root folder just run:

```
docker-compose exec php-fpm php artisan migrate && docker-compose exec php-fpm php artisan db:seed
```
**This will set up the database schema and seeds it with 50 random generated offices.**

##Queue

This project uses Redis as queue management driver. In order to have the queue system active, 
before using the project from inside the php-fpm container just run:

```
php artisan queue:work
```

##Caching

This project uses Redis as caching driver.

## Asynchronous tasks

### Access MySQL with phpMyAdmin

The development environment comes with phpMyAdmin already installed.
You can reach the management UI by browsing
to [this page](localhost:8888) and providing the 
credentials specified in the env keys (*MYSQL_DB_USERNAME*, *MYSQL_DB_PASSWORD*).

##Using The APIs

In order to use this project make sure that you have installed in your local machine a collaboration platform 
such as [Postman](https://www.postman.com/) and prepend to each request the **localhost/** base url.

The index page (localhost:8888) returns the Laravel defaul index page.

The return responses may be one o this three possibility:

- A Json (or a json collection containing a list of) representation of an Office instance:

```
{
  "id" : "id" The office id
  "name" : "name" The office name
  "address" : "address" The office address
}
```

- A success/error message structured as the following:

```
{
    "status" : "status" Http code status
    "message" : "status message" An error/success message
}

```
- A request model validation error

```
{
    "errors" : {
        "field" : {"error message"}
    } 
}
```

- ###/api/offices

Method: **GET**  
Payload: **null**
Response: Json array with all the offices in the database.

- ###/api/offices/{officeId})

Method: **GET**  
**OfficeId: integer**
Payload: **null**
Response: Json with desired office, a request model validation error or an error message.

- ###/api/offices/create/new)

Method: **POST**  
Payload: a json encoded key-value array with the following fields:  
-- name : the office name  
-- address : the office address
Response: A success message or a validation request model error.

- ###/api/offices/{officeId})

Method: **POST**  
**OfficeId: integer**
Payload: a json encoded key-value array with the following fields:  
-- name : the office name (nullable)
-- address : the office address (nullabe)
Response: A success message, a request model validation error or an error message.

- ###/api/offices/delete/{officeId})

Method: **POST**  
**OfficeId: integer**
Payload: **null**
Response: A success message, a request model validation error or an error message.

##Command Line

You may want to use the endpoints through command line rather that using postman.  
There's no need to specify any arguments because they will be asked to the user.  

 From inside the php-fpm container:
 
 - ### Get all offices
 
 ```
 php artisan offices:all
 ```
 
 - ### Get single office information
 
 ```
 php artisan offices:get
 ```

- ### Create office

```
php artisan offices:create
```

- ### Delete office

```
php artisan offices:delete
```

- ### Update office

```
php artisan offices:update
```


## Testing

### PHPUnit

In order to execute your integration tests simply run this command from inside your php-fpm container:

```
php artisan test --testsuite=Integration
```

In order to execute your functional tests simply run this command from inside your php-fpm container:

```
docker-compose exec php-fpm php artisan test --testsuite=Functional
```

In order to execute all the test suites simply run this command from inside your php-fpm container:
```
docker-compose exec php-fpm php artisan test
```

The PHPUnit configuration file is located in the `/` directory.

##Cs-fix

Normally I use php cs-fix to format the code according to be PSR-* compliant, but Laravel comes with style.ci that 
does this process (with all rules presetted)  automatically when creating pull requests.

## Future improving

Due to lack of time I couldn't included two features to this project:

- Including PHPStan for static code analysis
- Write test over the Redis queue
- Write test over the OfficeController 
