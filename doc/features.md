# Behat features

## Example usages

### API examples
See [Behat examples](https://github.com/ibexa/behat/tree/master/features/examples) to see how to use Behat sentences to:
- create Languages, Content items, Content Types
- create Users with complex permissions
- create given YAML configuration

### Logging into the repository

Use the [TestContext](../src/lib/API/Context/TestContext.php) to log in to the Repository and perform API calls as given user.

### Browser examples

Look at [AdminUI feature files](https://github.com/ibexa/admin-ui/tree/main/features/standard) to see example browser tests for AdminUI. If you want to reuse these Steps in your code in addition to the Context that defines them you also need to include:
- `Ibexa\Behat\Browser\Context\Hooks`
- `Ibexa\Behat\Browser\Context\BrowserContext`

#### Improved drag and drop

Selenium does not support drag and drop interactions between iframes. To achieve that you can use the `UtilityContext::moveWithHover` method (which also supports hover simulation between the actions). See the [drag-mock documentation](https://github.com/andywer/drag-mock#browser) (the library we use behind the scenes) for more information.

Before you start using that you need to inject the [drag-mock script](../src/bundle/Resources/public/js/scripts/drag-mock.js) into your templates: one way of doing this is described in [Webpack Encore configuration doc](https://doc.ibexa.co/en/master/administration/back_office/back_office_elements/importing_assets_from_bundle/#configuration-from-a-bundle). For an example see [ez.config.manager.js](../src/bundle/Resources/encore/ez.config.manager.js).

## Behat extension

### SiteAccess awareness

With `IbexaExtension` enabled Behat becomes SiteAccess aware. The SiteAccess used can be specified using `IBEXA_SITEACCESS` environment variable, otherwise the default SiteAccess will be used.

## Debugging tools

Use the `Ibexa\Behat\Browser\Context\DebuggingContext` Context class to access browser and server logs after Scenario failure, which combined with the screenshots feature makes debugging failures (even on CI) much easier.

## Default testing configuration

Behat might override some settings with values that are needed for testing. If you want to disable this behaviour you should set the `ibexa.behat.override_configuration` parameter to `false`.
