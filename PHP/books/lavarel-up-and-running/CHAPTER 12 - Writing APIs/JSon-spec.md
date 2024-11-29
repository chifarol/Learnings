<!-- @format -->

# The JSON API Spec

The JSON API is a standard for how to handle many of the most common tasks in building JSON-based APIs: filtering, sorting, pagination, authentication, embedding, linking, metadata, and more.

1. A document MUST contain at least one of the following top-level members
   • `data`: the document’s “primary data”
   • `errors`: an array of error objects
   • `meta`: a meta object that contains non-standard meta-information
