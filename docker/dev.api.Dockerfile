FROM debian:bullseye-slim

RUN apt update && apt -y install python3 python3-pip openssh-client

WORKDIR /api

COPY ./api /api
COPY ./docker/generate_jwt_keys.sh /scripts/generate_jwt_keys.sh

RUN python3 -m pip install --upgrade pip
RUN pip install --no-cache-dir .
RUN pip install .[dev]

EXPOSE 8555

CMD ["uvicorn", "main:app", "--reload", "--host", "0.0.0.0", "--port", "8555"]
