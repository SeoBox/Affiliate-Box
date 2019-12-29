#!/usr/bin/env bash
alias wp="docker-compose run --rm wpcli"

wp core install --url="localhost" --title="Blog" --admin_user=admin --admin_password="admin" --admin_email=admin@inkvi.com
wp theme install generatepress --activate
wp plugin install elementor advanced-custom-fields --activate
wp plugin install anywhere-elementor --activate
wp plugin install https://shop.webtechstreet.com/index.php?eddfile=58004%3A21%3A42%3A1&ttl=1577689124&file=42&token=726cef0017dc15cdcaabf284d01f0fa1ff1f2bafd68fbe8ec774009e533a3fde --activate
wp plugin install https://connect.advancedcustomfields.com/index.php?a=download&p=pro&k=b3JkZXJfaWQ9MTgxNzIwfHR5cGU9ZGV2ZWxvcGVyfGRhdGU9MjAxOS0xMi0yOCAyMToxNjoxOA==