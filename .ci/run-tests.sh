#!/bin/bash

echo "Generating random key name and value..."
export KEY_NAME=$(date | shasum | fold -w 8 | head -n 1)
export KEY_NAME="${MULTIDEV_NAME}_${KEY_NAME}"
export KEY_VALUE=$(date | shasum | fold -w 40 | head -n 1)

echo "Setting secret..."
terminus secret:set ${TERMINUS_SITE} --scope=web,user ${KEY_NAME} ${KEY_VALUE}

echo "Creating key..."
terminus drush ${TERMINUS_SITE}.${MULTIDEV_NAME} -- key:save --label="${KEY_NAME}" --description="Test key" --key-type="authentication" --key-provider="pantheon_secret" --key-provider-settings="{\"secret_name\": \"${KEY_NAME}\"}"

echo "Retrieving key..."
VALUE=$(terminus drush ${TERMINUS_SITE}.${MULTIDEV_NAME} -- key:value-get ${KEY_NAME} | awk 'NR==4 {print $0}')

echo "Checking key..."
if [ "$VALUE" != "No value set." ]; then
  echo "Key value is not empty"
  exit 1
fi

echo "Compare key with original value..."
if [ "$VALUE" != "$KEY_VALUE" ]; then
  echo "Key value is not equal to original value"
  exit 1
fi

echo "Delete test secret..."
terminus secret:delete ${TERMINUS_SITE} ${KEY_NAME}
