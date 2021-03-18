#!/usr/bin/env bash

mysqldump --insert-ignore --single-transaction=TRUE --column-statistics=0 --port=3357 --result-file=var/db.sql -h127.0.0.1 -umykhailok -picantbelieve mykhailok_local
gzip var/db.sql -f
git add var/db.sql.gz
