# CSV Transformation Pipeline

## Table of Contents

1. [Architecture Overview](#architecture-overview)
2. [SOLID and DRY Principles](#solid-and-dry-principles)
3. [Areas for Improvement](#areas-for-improvement)
4. [Setup and Usage](#setup-and-usage)

---

## Architecture Overview

The CSV Transformation Pipeline is designed to read data from a CSV file, apply a series of transformations to the data, and output the transformed data to a new CSV file. The architecture follows a layered (onion) approach, separating concerns across different components to enhance maintainability, scalability, and testability.

### Key Components

- **CLI Command (`TransformCsvCommand`)**: Acts as the entry point for the application when run from the command line. It handles input arguments, initializes components, and executes the transformation pipeline.

- **Transformation Pipeline (`TransformationPipeline`)**: Orchestrates the flow of data from the input provider through the transformation steps to the output handler.

- **Input Provider (`CsvInputProvider`)**: Reads data from the CSV file using the `CsvFileParser` and converts raw data into domain-specific `Record` and `Field` objects.

- **Output Handler (`CsvOutput`)**: Writes transformed data to the output CSV file using the `CsvFileWriter`.

- **Field Value Transformers**: Apply specific transformations to data fields, such as converting strings to integers, formatting dates, or multiplying numeric values.

- **Configuration (`DefaultPipelineConfig`)**: Defines the transformations and mappings to be applied to the data, allowing for flexible and reusable configurations.

### Justification of Choices

- **Separation of Concerns**: By dividing the application into distinct components, each responsible for a specific task, we ensure that changes in one part of the system have minimal impact on others.

- **Extensibility**: The use of interfaces and abstract classes allows for easy addition of new input providers, output handlers, or transformers without modifying existing code.

- **Reusability**: Common functionalities are encapsulated in reusable components, promoting code reuse and reducing duplication.

- **Testability**: Decoupled components can be independently unit tested, improving the reliability of the application.

- **Maintainability**: A clear architecture makes it easier to understand the system, identify bugs, and implement new features.

---

## SOLID and DRY Principles

### Single Responsibility Principle (SRP)

Each class in the application has a single responsibility, e.g.:

- **`CsvFileParser`**: Handles parsing of CSV files.
- **`CsvFileWriter`**: Manages writing data to CSV files.
- **Transformers**: Each transformer handles a specific type of data transformation.

### Open/Closed Principle (OCP)

Classes are open for extension but closed for modification:

- New transformers can be added without modifying existing ones.
- The pipeline can be reconfigured through the builder without altering core classes.

### Liskov Substitution Principle (LSP)

Components that implement common interfaces can be substituted without affecting the correctness of the program:

- Any class implementing `FieldValueTransformer` can be used in the pipeline.
- Input providers and output handlers can be swapped as long as they adhere to their interfaces.

### Interface Segregation Principle (ISP)

Interfaces are specific to what clients need:

- Separate interfaces for input providers (`InputProvider`) and output handlers (`OutputHandler`).
- Transformers implement the `FieldValueTransformer` interface, focusing only on transformation logic.

### Dependency Inversion Principle (DIP)

High-level modules do not depend on low-level modules but on abstractions:

- The pipeline depends on abstractions like `InputProvider` and `OutputHandler` rather than concrete implementations.

### Don't Repeat Yourself (DRY)

- Common functionality is abstracted into base classes or utility functions.
- The builder pattern prevents duplication in configuring the pipeline.
- Reusable transformers and value objects reduce code redundancy.

---

## Areas for Improvement

### Use of Dedicated CSV Processing Library

- **Explanation**: While the current implementation provides custom CSV parsing and writing, dedicated libraries like `League\Csv` offer robust handling of CSV-specific quirks, such as different delimiters, enclosure characters, and BOM handling.

- **Benefits**:
    - **Reliability**: Libraries are well-tested and handle edge cases effectively.
    - **Features**: Support for stream processing, handling large files, and flexible configurations.
    - **Maintenance**: Reduces the need to maintain custom parsing logic.

### Handling of BOM and Null Values

- **Current Approach**: Custom code handles BOM removal and replaces empty strings with `null`.
- **Improvement**: Utilizing a library would abstract away these concerns, ensuring consistent behavior across different environments.

### Configuration Flexibility

- **Enhancement**: Allowing configurations like delimiters, enclosure characters, and encoding to be specified via command-line options or configuration files.

### Error Handling and Logging

- **Suggestion**: Implement comprehensive logging to track the pipeline's execution and better error handling to provide more informative feedback to the user.

### Testing

- **Proposal**: Increase test coverage, including unit tests for individual components also in the infrastructure layer which at this moment is only tested via the functional test.

---

## Setup and Usage

### Prerequisites

- **Docker**: Ensure Docker is installed on your system.
- **Make**: Make sure you have `make` installed to use the provided Makefile commands.

### Setup Instructions

1. **Build the Docker Containers**

   ```bash
   make build
   ```

2. **Start the Containers**

   ```bash
   make up
   ```

3. **Run Tests**

   ```bash
   make tests
   ```

### Using the Application

To execute the CSV transformation command, you can either run it directly or by accessing the shell inside the Docker container.

#### Access the application shell

```bash
make sh
```

This command will give you access to the shell inside the Docker container.

#### Execute the Command Directly

Once inside the container shell, you can run the transformation command:

```bash
php bin/console app:transform:csv path/to/input.csv path/to/output.csv
```

**Example**:

```bash
php bin/console app:transform:csv data/input.csv data/output.csv
```

### Shutdown

After you're done using the application, you can stop and remove the Docker containers:

```bash
make down
```

---