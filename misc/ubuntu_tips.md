<!-- @format -->

## Upload Files from local

```
scp /path/to/local/file username@your_droplet_ip:/path/on/droplet/
```

```
rsync -avz -e ssh  /path/to/local/file username@your_droplet_ip:/path/on/droplet/
```

rsync -avz -e ssh bk.wpress root@143.198.149.41:/var/www/app.mobirevo.com/wp-content/ai1wm-backups

scp bk.wpress root@143.198.149.41:/var/www/app.mobirevo.com/wp-content/ai1wm-backups

## Check Disk Usage

```
ls -l, which only displays the size of the individual files in a directory, nor
df -h, which only displays the free and used space on my disks.

du -hs /path
```
