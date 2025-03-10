CREATE DATABASE IF NOT EXISTS btc_tracker_testing;

GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, DROP, ALTER
ON btc_tracker_testing.*
TO 'tracker_user'@'%';