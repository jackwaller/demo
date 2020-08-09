## API BACKEND

To get started, make sure you have [Docker installed](https://docs.docker.com/docker-for-mac/install/) on your system, and then clone this repository.

Next, navigate in your terminal to the directory you cloned this, and spin up the containers for the web server by running `docker-compose up -d --build site`.

Three additional containers are included that handle Composer, NPM, and Artisan commands *without* having to have these platforms installed on your local computer. Use the following command examples from your project root, modifying them to fit your particular use case.

Run these following steps

- Copy the .env.example to create a .env file
- Run in the terminal `docker-compose run --rm artisan migrate`
- Run in the terminal `docker-compose run --rm artisan passport:install`
- API available from http://localhost:8080;

## FRONTEND

Run the following command from the root of the project to build the frontend.

- cd frontend; ng serve;
- Application available from http://localhost:4200;