FROM debian:bullseye-slim

RUN apt update && apt -y install python3 python3-pip openssh-client

WORKDIR /api

COPY ./api/pyproject.toml /api/pyproject.toml

RUN python3 -m pip install --upgrade pip
RUN pip install --no-cache-dir .
RUN pip install .[dev]

COPY ./api /api

EXPOSE 8555

CMD ["uvicorn", "main:app","--proxy-headers", "--reload", "--host", "0.0.0.0", "--port", "8555"]
