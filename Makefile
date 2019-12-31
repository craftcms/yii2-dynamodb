REGION ?= us-east-1
CACHE_TABLE_NAME ?= cache-table-test
ENDPOINT_URL ?= http://localhost:8000

tables:
	aws --endpoint-url=${ENDPOINT_URL} --region=${REGION} \
	dynamodb create-table --table-name=${CACHE_TABLE_NAME} \
	--attribute-definitions=AttributeName=key,AttributeType=S \
	--key-schema=AttributeName=key,KeyType=HASH \
	--billing-mode=PAY_PER_REQUEST
