Feature: Disable Help center in user settings

  @admin @setup @helpCenter @javascript
  Scenario: Disable Help center in user settings
    Given I open Login page in admin SiteAccess
    When I log in as admin with password publish
    And I'm on Content view Page for root
    And I go to user settings
    When I disable Help center
    And I perform the "Save and close" action
    Then success notification that "User settings 'browsing' updated." appears
