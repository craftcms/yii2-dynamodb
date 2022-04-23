REGION ?= local
CACHE_TABLE_NAME ?= cache-test
SESSION_TABLE_NAME ?= session-test
QUEUE_TABLE_NAME ?= queue-test
ENDPOINT_URL ?= http://localhost:8000
AWS_ACCESS_KEY_ID = local
AWS_SECRET_ACCESS_KEY = local

configure:
	aws configure set aws_access_key_id ${AWS_ACCESS_KEY_ID}
	aws configure set aws_secret_access_key ${AWS_SECRET_ACCESS_KEY}
cache:
	aws --endpoint-url=${ENDPOINT_URL} --region=${REGION} \
	dynamodb create-table --table-name ${CACHE_TABLE_NAME} \
	--attribute-definitions \
		AttributeName=PK,AttributeType=S \
		AttributeName=SK,AttributeType=S \
	--key-schema \
		AttributeName=PK,KeyType=HASH \
		AttributeName=SK,KeyType=RANGE \
	--provisioned-throughput \
		ReadCapacityUnits=5,WriteCapacityUnits=5
sessions:
	aws --endpoint-url=${ENDPOINT_URL} --region=${REGION} \
	dynamodb create-table --table-name=${SESSION_TABLE_NAME} \
	--attribute-definitions \
		AttributeName=id,AttributeType=S \
	--key-schema \
		AttributeName=id,KeyType=HASH \
	--provisioned-throughput \
		ReadCapacityUnits=5,WriteCapacityUnits=5
queue:
	aws --endpoint-url=${ENDPOINT_URL} --region=${REGION} \
	dynamodb create-table --table-name=${QUEUE_TABLE_NAME} \
	--attribute-definitions \
		AttributeName=PK,AttributeType=S \
	--key-schema \
		AttributeName=PK,KeyType=HASH \
	--provisioned-throughput \
		ReadCapacityUnits=5,WriteCapacityUnits=5
tables: configure cache sessions queue
