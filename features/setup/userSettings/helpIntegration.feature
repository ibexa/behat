Feature: Setup help integration configuration

  @setup @help
  Scenario: Set up help configuration to disable help integration
    Given I create a file "config/packages/ibexa_integrated_help.yaml" with contents
    """
    ibexa_integrated_help:
        enabled: false

    """
