# Railway Connection Verification Checklist

## Status: Variables Found in MySQL Service ✓

You have MySQL variables:
- **MYSQLHOST**: mysql.railway.internal
- **MYSQLPORT**: 3306
- **MYSQLDATABASE**: railway
- **MYSQLUSER**: root
- **MYSQLPASSWORD**: vWUHBvNRCmiaKiFsS5iEBBjuJmpIpiaX

---

## Next Step: Check Web Service Variables

Go to: **IT-DMS (Web Service) → Variables tab**

### Look for these variables:

```
DB_HOST=mysql.railway.internal
DB_PORT=3306
DB_DATABASE=railway
DB_USERNAME=root
DB_PASSWORD=vWUHBvNRCmiaKiFsS5iEBBjuJmpIpiaX
```

### If NOT found, add them:

1. Click **Web Service** (IT-DMS) in left sidebar
2. Click **Variables** tab
3. Click **+ New Variable**
4. Add each:
   - `DB_HOST` = `mysql.railway.internal`
   - `DB_PORT` = `3306`
   - `DB_DATABASE` = `railway`
   - `DB_USERNAME` = `root`
   - `DB_PASSWORD` = `vWUHBvNRCmiaKiFsS5iEBBjuJmpIpiaX`

5. Click Deploy (or push to GitHub to trigger automatic deploy)

---

## What This Does

- Tells Laravel where to find the MySQL database
- Allows the app to connect and run migrations
- Prevents 502 Bad Gateway errors

---

## Confirm After Deploy

Check deployment logs in Railway for:
- ✓ "Migrations successfully completed"
- ✓ No "Connection refused" errors
- ✓ No "Unknown database" errors

If you see errors, screenshot the deployment logs and I'll help fix them.
