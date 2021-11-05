#!/usr/bin/env bash
PATH=/etc:/bin:/sbin:/usr/bin:/usr/sbin:/usr/local/bin:/usr/local/sbin

ETC_HOSTS=/etc/hosts

while read line;
do
read -ra ip <<< "$line"
if [[ "${ip[0]}" =~ ^(([0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])$ ]]
then
  ID=$(curl --location --request POST 'http://dns_api.loc/create' --form 'domain='"${ip[1]}"'' | cut -d'"' -f4)
  re='^[0-9]+$'
  if [[ $ID =~ $re ]]
  then
    curl --location --request POST 'http://dns_api.loc/create-record' \
    --form 'type="A"' \
    --form 'domain='"$ID"'' \
    --form 'name="some_name"' \
    --form 'val='"${ip[0]}"'' \
    --form 'ttl="5"'
    fi
fi
done < $ETC_HOSTS