# Changelog

## [Unreleased]

### Added

- Support for `MorphMany` relationships in `CuratorPicker`.
- Method to handle state hydration and saving for `MorphMany` relationships.
- Handling for related media data retrieval in `loadStateFromRelationshipsUsing`.

### Changed

- Modified `saveRelationshipsUsing` method to correctly manage `MorphMany` relationships, including ordering.
- Updated `getRelationship` method to recognize `MorphMany`.

This changelog provides an overview of the modifications made to support `MorphMany` relationships in the `CuratorPicker` component.
