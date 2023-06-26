# Requirements

Python > 3.11
poetry
python-devel

# Install dependencies

```
poetry install
poetry config virtualenvs.in-project true # this creates .venv folder in the project
```

## Setting up the DB and running migrations

Activate the env:

```
poetry shell
```

Run the migrations with

```
alembic upgrade head
```

Create a new migration with

```
alembic revision --autogenerate -m "Migrations description"
```

It's also possible to run scripts without activating the env

```
poetry run alembic revision --autogenerate -m "Migrations description"
```

For more details check the alembic documentation.

## Run FastAPI

Inside the `api` folder, make sure you have the virtual environment
activated and all the dependencies installed with `poetry shell` and
`poetry install`.

Start the server with `uvicorn main:app --reload --host localhost --port 8555`
