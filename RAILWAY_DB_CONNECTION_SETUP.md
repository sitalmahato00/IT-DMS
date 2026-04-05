# Railway Database Connection Setup

Your database is shown as **Online** in Railway, but the app needs proper credentials.

## Required Railway Variables to Set

Go to: **IT-DMS Project → Variables tab** and add these:

```
APP_NAME=IT-DMS
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:Yqish7Z0NkseXojofYPGkl1s7/63VewLPVuU838slis=
APP_URL=https://it-dms-production.up.railway.app

DB_CONNECTION=mysql
DB_HOST=${{ MySQL.MYSQL_HOST }}
DB_PORT=${{ MySQL.MYSQL_PORT }}
DB_DATABASE=${{ MySQL.MYSQL_DATABASE }}
DB_USERNAME=${{ MySQL.MYSQL_USER }}
DB_PASSWORD=${{ MySQL.MYSQL_PASSWORD }}

SESSION_DRIVER=file
CACHE_DRIVER=file
QUEUE_CONNECTION=database
LOG_CHANNEL=stack
```

## Variables Already set by Railway:
- `${{ MySQL.MYSQL_HOST }}` - Auto-injected from MySQL service
- `${{ MySQL.MYSQL_PORT }}` - Auto-injected from MySQL service  
- `${{ MySQL.MYSQL_DATABASE }}` - Auto-injected from MySQL service
- `${{ MySQL.MYSQL_USER }}` - Auto-injected from MySQL service
- `${{ MySQL.MYSQL_PASSWORD }}` - Auto-injected from MySQL service

## How to Verify in Railway:
1. Click **MySQL** service (bottom left in screenshot)
2. Go to **Variables** tab
3. You should see the auto-generated variables like:
   - MYSQL_HOST
   - MYSQL_PORT
   - MYSQL_DATABASE
   - MYSQL_USER
   - MYSQL_PASSWORD

4. Click **Web Service** (IT-DMS)
5. Go to **Variables** tab
6. Add the DB_* variables referencing the MySQL service variables

## Steps to Fix:
1. Go to Railway Dashboard
2. Click IT-DMS project
3. Click **Variables** tab
4. Add all the variables above
5. Redeploy (clicking Deploy button or push to GitHub)
6. Check deployment logs for connection errors
