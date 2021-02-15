#!/usr/bin/env bash

mysqldump --insert-ignore --single-transaction=TRUE --port=3357 --result-file=var/db.sql -h127.0.0.1 -uroot -p mykhailok_local
gzip var/db.sql
git add var/db.sql
