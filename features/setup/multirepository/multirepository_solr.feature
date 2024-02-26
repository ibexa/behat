Feature: Multirepository setup for testing with Solr

  @multirepository
  Scenario: Set up new connection and make the default one unaccessible
    Given I append configuration to "parameters" in "config/packages/ibexa_solr.yaml"
    """
        solr_dsn_invalid: 'INVALID'
        solr_core_invalid: 'INVALID'
    """
    And I set configuration to "ibexa_solr" in "config/packages/ibexa_solr.yaml"
    """
        endpoints:
            endpoint0:
                dsn: '%solr_dsn%'
                core: '%solr_core%'
            endpoint1_invalid:
                dsn: '%solr_dsn_invalid%'
                core: '%solr_core_invalid%'
        connections:
            default:
                entry_endpoints:
                    - endpoint1_invalid
                mapping:
                    default: endpoint1_invalid
            second_connection:
                entry_endpoints:
                    - endpoint0
                mapping:
                    default: endpoint0
    """
