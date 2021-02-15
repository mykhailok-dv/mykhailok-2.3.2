#!/usr/bin/env bash

gunzip -c var/db.sql.gz > var/db.sql
mysql -uroot -proot -h127.0.0.1 --port="3357" -e"source var/db.sql" --show-warnings mykhailok_local;
rm var/db.sql
