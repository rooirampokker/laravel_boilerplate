#!/usr/bin/env bash

if [[ -z "$CIRCLECI" ]]; then
    echo "This script can only be run by CircleCI. Aborting." 1>&2
    exit 1
fi

echo "compressing installation for deployment..."
cd ~/project
tar zcf - ./* | ssh -o StrictHostKeyChecking=no ${STAGING_SSH_USERNAME}@${STAGING_SSH_URL} "cat > project.tar.gz"

echo "untar compressed project..."
ssh -o StrictHostKeyChecking=no ${STAGING_SSH_USERNAME}@${STAGING_SSH_URL} << EOF
mv project.tar.gz ${STAGING_WEB_ROOT}
cd ${STAGING_WEB_ROOT}
tar -xzf project.tar.gz
#rm project.tar.gz
#cp .env.example .env
#php artisan migrate

EOF
