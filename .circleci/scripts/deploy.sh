#!/usr/bin/env bash

if [[ -z "$CIRCLECI" ]]; then
    echo "This script can only be run by CircleCI. Aborting." 1>&2
    exit 1
fi

echo "compressing installation for deployment..."
tar zcf - project | ssh -o StrictHostKeyChecking=no ${PROD_SSH_USERNAME}@${PROD_SSH_URL} "cat > project.tar.gz"

echo "untar compressed project..."
ssh -o StrictHostKeyChecking=no albrecht.uk.com@ssh.gb.stackcp.com << EOF
mv project.tar.gz ${PROD_WEB_ROOT}
cd ${PROD_WEB_ROOT}
tar -xzf project.tar.gz --strip-components=1 -C ./
rm project.tar.gz
mv .env.production .env
php artisan migrate

EOF
