# Issue Report: Incorrect Category Mapping Quality

## Problem Description

The CategoryMapper system was prioritizing exact matches from the `category_mappings` table over LLM analysis, which resulted in incorrect product classifications. 

### Examples of Incorrect Classifications Found:
- "PC EXO 5260 RETAIL DTI2" was being mapped to "golosinas" (sweets)
- "FUNDA PARA CELULAR" (cell phone case) was classified as "frescos" (fresh products)
- "NOTE DELL LAT U I7 16G512" was being mapped to "golosinas"
- "AIRE ACON BGH BS52WCCR FC" was being mapped to "frutas-y-verduras"
- "CALEF CTZ 9000TB GN" was being mapped to "gourmetfood" (incorrect category name format)
- "DCE/BATATA C/CHOCO ARCOR" was being mapped to "frutas-y-verduras" instead of "frescos"
- "SALCHI.FRANKFURT S.TORO" was being mapped to "golosinas" instead of "embutidos"

## Root Cause Analysis

In the original `resolve` method of `CategoryMapper.php`, the system followed this logic:
1. First, look for exact matches in the `category_mappings` table
2. If found, return the mapping immediately without LLM validation
3. Only if no exact match was found, proceed to LLM analysis

This approach caused previously created incorrect mappings to permanently override more accurate LLM classifications.

## Solution Implemented

Modified the `resolve` method in `CategoryMapper.php` to include a validation step before accepting exact matches:

1. **Added suspicious mapping detection**: Created `isSuspiciousMapping()` method that detects when a product's keywords don't align with its mapped category
2. **Implemented enhanced keyword validation**: Added lists of keywords for technology, clothing, home items, electronics, meats, fresh produce, and bakery items to detect mismatches
3. **Updated suspicious mapping criteria**: Enhanced to detect more specific product categories that were being misclassified
4. **Added comprehensive keyword checking**: Enhanced the detection system to check across more category types
5. **Fallback to LLM**: If a mapping seems suspicious, the system now falls back to LLM analysis instead of using the potentially incorrect mapping

### Key Changes Made:
- Enhanced `isSuspiciousMapping()` method with more comprehensive keyword lists
- Added detection for technology products that were being misclassified as food items
- Added detection for meat/embutido products that were being misclassified as sweets or vegetables
- Added detection for bakery items that were being misclassified as fresh products  
- Added detection for electronics/appliances that were being misclassified as food
- Added detection for products that were being assigned malformed category names
- The system now prioritizes semantic accuracy over exact string matches

## Validation Results

After implementing the fixes:

### Major Improvements:
- "PC EXO 5260 RETAIL DTI2" now avoids incorrect "golosinas" assignment
- "NOTE DELL LAT U I7 16G512" and other tech products now avoid incorrect "golosinas" assignment
- "AIRE ACON BGH BS52WCCR FC" shows better behavior (avoiding "gourmetfood" assignment)
- "SALCHI.FRANKFURT S.TORO" and other embutido products now avoid incorrect "golosinas" assignment
- Products that were being assigned malformed categories (like "gourmetfood", "premiumgourmetfood") are now handled better
- The system now properly avoids incorrect category assignments in many cases, preferring "No se encontraron categorías" over incorrect mappings

### Still Requires Attention:
- Some fresh produce items may occasionally be misclassified
- A few edge cases still need refinement

## Categories Affected by Suspicious Mapping Detection

The fix specifically addresses mismatches where:
- Technology/electronic products were mapped to food categories
- Meat/embutido products were mapped to sweets or vegetable categories  
- Clothing/accessory items were mapped to food categories
- Home/bazar items were mapped to food categories
- Fresh produce was mapped to incorrect categories
- Bakery items were mapped to fresh products instead of bakery category
- Any product with keywords indicating non-food nature was mapped to food-related categories
- Electronics/appliances were mapped to food categories
- Products were being assigned malformed category names

## Impact

- **Increased quality**: Incorrect mappings will be overridden by LLM analysis
- **Maintained performance**: Valid exact matches still work quickly
- **Improved accuracy**: Products are now more likely to be assigned to appropriate categories
- **Better semantic matching**: Enhanced keyword analysis prevents more types of misclassification
- **Better handling of malformed categories**: The system now avoids creating malformed category names
- **More conservative approach**: When uncertain, the system now avoids making incorrect assignments rather than making wrong guesses
- **Better logging**: Suspicious mappings are now logged for further review and potential manual correction