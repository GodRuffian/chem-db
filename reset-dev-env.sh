#! /bin/bash

sed -i "s#172.17.0.1#172.20.0.1#" $PWD/raw/config/cache.yml
sed -i "s#mysql.docker.local#172.20.0.1#" $PWD/raw/config/database.yml
sed -i "s#username: username#username: genee#" $PWD/raw/config/database.yml
sed -i "s#password: password#password: 83719730#" $PWD/raw/config/database.yml
sed -i "s#rd.labmai.com#172.20.0.1#" $PWD/raw/config/system.yml
