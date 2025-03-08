# Metricalo Assignment
## Setup
1. Clone the repository using `git clone  https://github.com/ahmed-bermawy/metricalo_assignment.git`
2. Go to the project directory using `cd metricalo_assignment`
3. Run `composer install` to install the dependencies
4. serve the application using `symfony local:server:start`

## Testing Endpoints
To test the endpoints, you can use the postman collection provided in the root directory of the project `Metricalo.postman_collection.json`

## Testing Commands
To test the commands, you can run the following commands:
`bin/console app:process-payment` to process the payments

## Running Tests Suite
To run the unit tests, you can run the following command: `./vendor/bin/phpunit`