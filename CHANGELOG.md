### CHANGELOG ###

##### v0.2.1 (2013-08-04) #####
- Adding registration of alias for filter, available in rule definition.
- Adding filters prepended or appended for every rule.

##### v0.2.0 (2013-08-04) #####
- [BCB] Remove or modified methods:
    - `Rule::addSourceProperty` - removed
    - `Rule::setSourceProperties` - removed
    - `Rule::getSourceProperties` - renamed `getSourcePaths`
    - `Rule::addFilter` - removed
    - `Rule::setFilters` - removed
    - `Rule::setTargetProperty` - removed
    - `Rule::getTargetProperty` - renamed `getTargetPath`
    - `Rule::addRule()` - removed
    - `RuleSet::addRule` - requires two parameters `sourcePaths` and `targetPath`, allows third parameter `filters`
- Cleanup interface.
- 100% code coverage.

##### v0.1.0 (2013-02-24) #####
- Basic version, allows transformation from array or object into object. Does not
support collections inside source or target object.