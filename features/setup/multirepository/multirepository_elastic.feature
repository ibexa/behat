Feature: Multirepository setup for testing with Elastic

  @elastic
  Scenario: Set up new connection and make the default one unaccessible
    Given I append configuration to "parameters" in "config/packages/ibexa_elasticsearch.yaml"
    """
        elasticsearch_dsn_invalid: "INVALID"
    """
    And I set configuration to "ibexa_elasticsearch.connections" in "config/packages/ibexa_elasticsearch.yaml"
    """
        default:
            hosts:
                - "%elasticsearch_dsn_invalid%"
        second_connection:
            hosts:
                - "%elasticsearch_dsn%"
    """
