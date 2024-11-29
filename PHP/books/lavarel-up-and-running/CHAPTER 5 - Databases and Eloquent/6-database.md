<!-- @format -->

## Inspecting Your Database

If you want to dig into the status or definition of your database, its tables, and its
models, there are a few Artisan commands for exactly that purpose

- **db:show** - Shows a table overview of your entire database, including the connection details, tables, size, and open connections
- **db:table {tableName}** - Passed a table name, shows the size and lists the columns
- **db:monitor** - List, the number of open connections to the database
