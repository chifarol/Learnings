<!-- @format -->

## Resolve Error: Failed to open stream: Permission denied

```
sudo chown -R $USER:www-data storage
sudo chown -R $USER:www-data bootstrap/cache

chmod -R 775 storage
chmod -R 775 bootstrap/cache
```
