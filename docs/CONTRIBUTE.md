If you would like to make a contribution to [Semantic MediaWiki][smw] (a.k.a. SMW), please ensure that pull requests
are based on the current master. See also the [developer documentation overview][smwddo].

In order to swiftly coordinate a contribution, the following should be provided:
- Definition that complies with the existing `json` file
- Code should be easily read and if necessary be put into separate components (or classes)
- Newly added features should not alter an existing test but instead provide additional test coverage to verify the
expected behaviour

For a description on how to write and run PHPUnit test, please consult the [manual][mw-testing].

[smw]: https://github.com/SemanticMediaWiki/SemanticMediaWiki
[smwddo]: https://github.com/SemanticMediaWiki/SemanticMediaWiki/blob/contribute/docs/technical/README.md
[mw-testing]: https://www.mediawiki.org/wiki/Manual:PHP_unit_testing
