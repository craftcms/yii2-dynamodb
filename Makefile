REGION ?= local
CACHE_TABLE_NAME ?= cache-test
SESSION_TABLE_NAME ?= session-test
QUEUE_TABLE_NAME ?= queue-test
ENDPOINT_URL ?= http://localhost:8000
AWS_ACCESS_KEY_ID = local
AWS_SECRET_ACCESS_KEY = local

tables:
	aws --endpoint-url=${ENDPOINT_URL} --region=${REGION} \
		dynamodb create-table --table-name=${CACHE_TABLE_NAME} \
		--attribute-definitions=AttributeName=id,AttributeType=S \
		--key-schema=AttributeName=id,KeyType=HASH \
		--billing-mode=PAY_PER_REQUEST && \
	aws --endpoint-url=${ENDPOINT_URL} --region=${REGION} \
    	dynamodb create-table --table-name=${SESSION_TABLE_NAME} \
    	--attribute-definitions=AttributeName=id,AttributeType=S \
    	--key-schema=AttributeName=id,KeyType=HASH \
    	--billing-mode=PAY_PER_REQUEST && \
    aws --endpoint-url=${ENDPOINT_URL} --region=${REGION} \
    	dynamodb create-table --table-name=${QUEUE_TABLE_NAME} \
    	--attribute-definitions=AttributeName=id,AttributeType=S \
    	--key-schema=AttributeName=id,KeyType=HASH \
    	--billing-mode=PAY_PER_REQUEST
