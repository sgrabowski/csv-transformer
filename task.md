Problem Statement
=================

We need to develop a PHP console application that transforms input CSV 
datasets into output CSV files of different formats. 
The application must allow users to define transformation 
options for each column, such as renaming columns and applying 
transformations like value recoding, calculations, and date formatting. 
Additionally, the solution should be designed to be easily 
extendable for future data sources and transformations.

Hard Requirements
=================

1.  **PHP ConsoleApplication**: Develop a console application in PHP that transforms data from an input CSV to an output CSV while applying user-defined transformations.
2.  **Flexible Importing**:
    *   Must handle source files with different column orders and names.
    *   Allow users to define transformation options for each incoming column, including:
        *   **Target Column Name**: The desired name of the column in the output CSV.
        *   **Transformations**: Any data transformations to apply (e.g., formatting dates, recoding values).
3.  **Implement Specific Transformations**:
    *   **Recode Values**: Map specific input values to new values (e.g., converting textual values to numeric codes).
    *   **Calculate**: Perform arithmetic operations on input values (e.g., multiply by 10).
    *   **Transform Date**: Change the format of date values to match the required output format.
4.  **Future-Proof Design**:
    *   Ensure the codebase can be extended to import data from other sources like Excel, RSS feeds, or API endpoints.
    *   Make it easy to add new types of transformations without significant changes to the existing code.
5.  **Automated Testing**:
    *   Create automated tests using PHPUnit or a similar testing framework.
    *   Use the provided input.csv and output.csv files for integration tests.
6.  **Documentation**:
    *   Provide a README.md file explaining the application's architecture and design choices.
    *   Justify how SOLID and DRY principles are applied.
    *   Include instructions on how to install and execute the application.
7.  **Best Practices**:
    *   Focus on code structure, scalability, testability, software design, readability, extensibility, and documentation.

Proposed Solution
=================
### Definitions
* **Field**: represent a single piece of data along with its associated identifier, such as a column name or key. It encapsulates both the value and its metadata (like the column name), making it suitable for representing:
  * A value in a CSV file
  * A cell in an Excel file
  * A value at the intersection of a row and column in a database table
* **Record** represents a collection of fields that together constitute a single logical unit of data. This concept is applicable across various data formats and storage systems, making it an ideal abstraction for:
  * A row in a CSV file
  * A row in an Excel spreadsheet
  * A row in a database table
  * A single entry in a JSON or XML dataset

### Flow
Create a configurable pipeline consisting of more or less these steps:
* Input Reader
  * Reads input from a file, feed, api, etc. Handles I/O operations and errors.
* Input Parser
  * Transforms the input values into Record objects
* Input Validator
  * Validates Field objects in each Record
* Field Value Transformer
  * Transforms the field value to another format or type.
* Field Metadata Transformer
  * Changes the field metadata, such as the column name for the output.
* Output Serializer
  * Serializes the resulting Records / Rows into desired output format
