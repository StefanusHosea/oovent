#!/bin/bash
# OOvent Plugin Validation Script

echo "========================================="
echo "OOvent Plugin Validation"
echo "========================================="
echo ""

# Check PHP syntax
echo "1. Checking PHP syntax..."
ERROR=0
for file in $(find . -name "*.php" -not -path "./.git/*"); do
    php -l "$file" > /dev/null 2>&1
    if [ $? -ne 0 ]; then
        echo "   ❌ Syntax error in: $file"
        ERROR=1
    fi
done

if [ $ERROR -eq 0 ]; then
    echo "   ✅ All PHP files have valid syntax"
fi
echo ""

# Check required files
echo "2. Checking required files..."
REQUIRED_FILES=(
    "oovent.php"
    "README.md"
    "LICENSE.txt"
    "uninstall.php"
    "includes/class-oovent-install.php"
    "includes/class-oovent-event-product.php"
    "includes/class-oovent-ticket.php"
    "includes/class-oovent-qr-code.php"
    "includes/class-oovent-checkin.php"
    "includes/class-oovent-email.php"
    "includes/class-oovent-reports.php"
    "includes/class-oovent-ajax.php"
    "includes/admin/class-oovent-admin.php"
    "includes/admin/class-oovent-scanner.php"
    "includes/admin/class-oovent-dashboard.php"
)

for file in "${REQUIRED_FILES[@]}"; do
    if [ -f "$file" ]; then
        echo "   ✅ $file"
    else
        echo "   ❌ Missing: $file"
        ERROR=1
    fi
done
echo ""

# Check directory structure
echo "3. Checking directory structure..."
REQUIRED_DIRS=(
    "includes"
    "includes/admin"
    "assets"
    "assets/css"
    "assets/js"
)

for dir in "${REQUIRED_DIRS[@]}"; do
    if [ -d "$dir" ]; then
        echo "   ✅ $dir/"
    else
        echo "   ❌ Missing: $dir/"
        ERROR=1
    fi
done
echo ""

# Count files
echo "4. Plugin Statistics..."
PHP_COUNT=$(find . -name "*.php" -not -path "./.git/*" | wc -l)
JS_COUNT=$(find . -name "*.js" -not -path "./.git/*" | wc -l)
CSS_COUNT=$(find . -name "*.css" -not -path "./.git/*" | wc -l)
MD_COUNT=$(find . -name "*.md" -not -path "./.git/*" | wc -l)

echo "   📄 PHP files: $PHP_COUNT"
echo "   📄 JavaScript files: $JS_COUNT"
echo "   📄 CSS files: $CSS_COUNT"
echo "   📄 Documentation files: $MD_COUNT"
echo ""

# Summary
echo "========================================="
if [ $ERROR -eq 0 ]; then
    echo "✅ Plugin validation PASSED"
    echo "========================================="
    exit 0
else
    echo "❌ Plugin validation FAILED"
    echo "========================================="
    exit 1
fi
