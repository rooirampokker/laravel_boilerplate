#!/usr/bin/env bash

if [[ -z "$CIRCLECI" ]]; then
    echo "This script can only be run by CircleCI. Aborting." 1>&2
    exit 1
fi

echo "compressing installation for deployment..."
tar zcf - ~/project | ssh -o StrictHostKeyChecking=no ${PROD_SSH_USERNAME}@${PROD_SSH_URL} "cat > project.tar.gz"

echo "untar compressed project..."
ssh -o StrictHostKeyChecking=no albrecht.uk.com@ssh.gb.stackcp.com << EOF
mv project.tar.gz ${PROD_WEB_ROOT}
cd ${PROD_WEB_ROOT}
tar -xzf project.tar.gz --strip-components=3 -C ./
rm project.tar.gz

echo "update .env with variables..."
mv .env.production .env

export PROD_DB_USER=${PROD_DB_USER} | echo $PROD_DB_USER
export PROD_DB_PASSWORD=${PROD_DB_PASSWORD} | echo $PROD_DB_PASSWORD
export PROD_DB_HOST=${PROD_DB_HOST} | echo $PROD_DB_HOST
export PROD_DB_SCHEMA=${PROD_DB_SCHEMA} | echo $PROD_DB_SCHEMA

php artisan key:generate
php artisan migrate

EOF
