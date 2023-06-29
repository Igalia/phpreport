# Requirements

Python > 3.11
Pip
virtualenv

# Install dependencies

Activate the env and install the dependencies

```
virtualenv .env
source .env/bin/activate
pip install .
```

## Setting up the DB and running migrations

Run the migrations with

```
alembic upgrade head
```

Create a new migration with

```
alembic revision --autogenerate -m "Migrations description"
```

For more details check the alembic documentation.

## Run FastAPI

Inside the `api` folder, make sure you have the virtual environment
activated and all the dependencies installed.

Start the server with `uvicorn main:app --reload --host localhost --port 8555`
