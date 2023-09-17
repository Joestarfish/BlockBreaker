# BlockBreaker

A PMMP plugin that allows you to choose what items can break specific blocks or prevent some blocks from being destroyed

[![](https://poggit.pmmp.io/shield.api/BlockBreaker)](https://poggit.pmmp.io/p/BlockBreaker)
[![](https://poggit.pmmp.io/shield.dl.total/BlockBreaker)](https://poggit.pmmp.io/p/BlockBreaker)

# Configuration

Inside of the `plugin_data/BlockBreaker/config.yml` file, you may change the following:

-   **do-break-block** - If the item cannot break the block, do we destroy it anyway or do we prevent is's destruction ?
-   **do-drop-xp** - Do we want to drop the XP after destroying a block ?
-   **blocks** - A list of block with the different items that can break it. Format: `<block_name>: <items_list>`
-   **items** - A list of items with the blocks they can break. Format: `<item_name>: <blocks_list>`
