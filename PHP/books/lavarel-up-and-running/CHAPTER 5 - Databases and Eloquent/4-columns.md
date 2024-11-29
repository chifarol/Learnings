<!-- @format -->

# Creating columns

```php
Schema::create('users', function (Blueprint $table) {
 $table->string('name');
});
```

## List of methods available on Blueprint instances for creating columns

- **id()**: An alias for $table->bigIncrements('id')
- **increments(colName), tinyIncrements(colName), smallIncrements(colName), mediumIncrements(colName), bigIncrements(colName)**: Adds an unsigned incrementing INTEGER primary key ID, or one of its many variations
- **enum(colName, [choiceOne, choiceTwo])**: Adds an ENUM column, with provided choices
- **boolean(colName)**: Adds a BOOLEAN type column (a TINYINT(1) in MySQL)
- **string(colName, length)**: Adds a VARCHAR type column with an optional length
- **char(colName, length)**: Adds a CHAR column with an optional length
- **integer(colName), tinyInteger(colName), smallInteger(colName), mediumInteger(colName), bigInteger(colName), unsignedTinyInteger(colName), unsignedSmallInteger(colName), unsignedMediumInteger(colName), unsignedBigInteger(colName)**: Adds an INTEGER type column, or one of its many variations
- **decimal(colName, precision, scale), unsignedDecimal(colName, precision, scale)**: Adds a DECIMAL column, with precision and scale—for example, decimal('amount', 5, 2) specifies a precision of 5 and a scale of 2; for an unsigned column, use the unsignedDecimal method
- **double(colName, total digits, digits after decimal)**: Adds a DOUBLE column—for example, double('tolerance', 12, 8) specifies 12 digits long, with 8 of those digits to the right of the decimal place, as in 7204.05691739
- **float(colName, precision, scale)**: Adds a FLOAT column (same as double in MySQL)
- **binary(colName)**: Adds a BLOB type column
- **foreignId(colName), foreignUuid(colName)**: Adds an UNSIGNED BIGINT or a UUID column, with provided choices
- **foreignIdFor(colName)**: Adds an UNSIGNED BIG INT column with the name colName
- **geometry(colName), geometryCollection(colName)**: Adds a GEOMETRY or a GEOMETRYCOLLECTION column
- **ipAddress(colName)**: Adds a VARCHAR column
- **json(colName), jsonb(colName)**: Adds a JSON or JSONB column
- **lineString(colName), multiLineString(colName)**: Adds a LINESTRING or MULTILINESTRING column with the given colName
- **text(colName), tinyText(colName), mediumText(colName), longText(colName)**: Adds a TEXT column (or its various sizes)
- **macAddress(colName)**:Adds a MACADDRESS column in the databases that support it (like PostgreSQL); on other database systems, it creates a string equivalent
- **point(colName), multiPoint(colName), multiPolygon(colName), polygon(colName)**: Adds columns of the types MULTIPOINT, MULTIPOLYGON, POLYGON, and POINT, respectively
- **set(colName, membersArray)**: Creates a SET column with the colName name and membersArray as members
- **time(colName, precision), timeTz(colName, precision)**: Adds a TIME column with colName name; for time zone awareness, use the `timeTz()` method

- **timestamp(colName, precision), timestampTz(colName, precision)**: Adds a TIMESTAMP column; for time zone awareness, use the `timestampTz()` method
- **timestamps(precision), nullableTimestamps(precision), timestampsTz(precision)**: Adds `created_at` and `updated_at` timestamp columns with optional precision, nullable, and time zone–aware variations
- **uuid(colName)**: Adds a UUID column (CHAR(36) in MySQL)
- **rememberToken()**: Adds a remember_token column (VARCHAR(100)) for user “remember me” tokens
- **year()**: Adds a YEAR column
- **softDeletes(colName, precision), softDeletesTz(colName, precision)**: Adds a `deleted_at` timestamp for use with soft deletes with optional precision and time zone–aware variations

- **morphs(colName), nullableMorphs(colName), uuidMorphs(relationshipName), nullableUuidMorphs(relationshipName)**: For a provided colName, adds an integer colName_id and a string colName_type(e.g., morphs(tag) adds integer tag_id and string tag_type); for use in polymorphic relationships, using IDs or UUIDs, and can be set as nullable as per method name

## Building extra properties uently

More properties/constraints can be chained

- **nullable()** - allows NULL values to be inserted into this column
- **default('default content')** - Specifies the default content for this column if no value is provided
- **unsigned()** - Marks integer columns as unsigned (not negative or positive, but just an integer)
- **first()** - Places the column first in the column order
- **after(colName)** - Places the column after another column in the column order
- **charset(charset)** - Sets the charset for a column
- **collation(collation)** - Sets the collation for a column
- **invisible()** - Makes the column invisible to `SELECT` queries
- **useCurrent()** - Used on TIMESTAMP columns to use CURRENT_TIMESTAMP as the default value
- **isGeometry()** - Sets a column type to GEOMETRY (the default is GEOGRAPHY)
- **unique()** - Adds a UNIQUE index
- **primary()** - Adds a primary key index
- **index()** - Adds a basic index

## Modifying columns

- just write the code you would write to create the column as if it were new, and then append a call to the `change()` method after it

```php
Schema::table('users', function (Blueprint $table) {
    $table->string('name', 100)->change();
    $table->string('deleted_at')->nullable()->change();
});
```

Other useful column methods are:

- **renameColumn('old_col_name', 'new_col_name')**: rename column
- **dropColumn('col_name')**: rename column

### Modifying Multiple Columns at Once in SQLite

- If you try to drop or modify multiple columns within a single migration closure and you are using SQLite, you’ll run into errors.
- to solve this create multiple calls to `Schema::table()` within the `up()` method of your migration

```php
public function up(): void
{
    Schema::table('contacts', function (Blueprint $table)
    {
        $table->dropColumn('is_promoted');
    });
    Schema::table('contacts', function (Blueprint $table)
    {
        $table->dropColumn('alternate_email');
    });
}
```
