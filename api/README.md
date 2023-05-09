# Requirements

Python > 3.11
poetry
python-devel

# Install dependencies

```
poetry install
poetry config virtualenvs.in-project true # this creates .venv folder in the project
```

Before using alembic, need to add python path so it can find the other folders, so inside the folder `api`, run `export PYTHONPATH=.` -> need to check if there's a better way

- Activate the env

```
poetry shell # activates env
```

OR it's possible to run scripts without activating the env

```
poetry run alembic revision --autogenerate -m "Added account table"
```
