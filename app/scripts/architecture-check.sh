#!/bin/bash
# Architecture enforcement checks (v2 - extended)
# Run: bash scripts/architecture-check.sh

ERRORS=0
WARNINGS=0

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo "=== Architecture Checks ==="

# ─────────────────────────────────────────────────────────────
# LAYER BOUNDARY CHECKS
# ─────────────────────────────────────────────────────────────

echo ""
echo "── Layer Boundaries ──"

# 1. Repositories must not import ViewModels
echo "Checking: Repositories must not import ViewModels..."
REPO_VM=$(grep -r "use App\\\\ViewModels" app/src/Repositories/ 2>/dev/null)
if [ -n "$REPO_VM" ]; then
    echo -e "${RED}❌ FAIL: Repository imports ViewModel${NC}"
    echo "$REPO_VM"
    ERRORS=$((ERRORS + 1))
else
    echo -e "${GREEN}✅ PASS${NC}"
fi

# 2. Controllers must not use Repositories directly
echo "Checking: Controllers must not import Repositories..."
REPO_IMPORTS=$(grep -rn "use App\\\\Repositories\\\\" app/src/Controllers/ 2>/dev/null)
if [ -n "$REPO_IMPORTS" ]; then
    echo -e "${RED}❌ FAIL: Controller imports Repository directly:${NC}"
    echo "$REPO_IMPORTS"
    ERRORS=$((ERRORS + 1))
else
    echo -e "${GREEN}✅ PASS${NC}"
fi

# 3. Controllers must not have $this->*Repository property access
echo "Checking: Controllers must not access \$this->*Repository..."
REPO_ACCESS=$(grep -rn '\$this->[a-zA-Z]*Repository' app/src/Controllers/ 2>/dev/null)
if [ -n "$REPO_ACCESS" ]; then
    echo -e "${RED}❌ FAIL: Controller accesses Repository property:${NC}"
    echo "$REPO_ACCESS"
    ERRORS=$((ERRORS + 1))
else
    echo -e "${GREEN}✅ PASS${NC}"
fi

# 4. ViewModels must not import Repositories or Services
echo "Checking: ViewModels must not import Repositories/Services..."
VM_IMPORTS=$(grep -rE "use App\\\\(Repositories|Services)" app/src/ViewModels/ 2>/dev/null)
if [ -n "$VM_IMPORTS" ]; then
    echo -e "${RED}❌ FAIL: ViewModel imports Repository or Service${NC}"
    echo "$VM_IMPORTS"
    ERRORS=$((ERRORS + 1))
else
    echo -e "${GREEN}✅ PASS${NC}"
fi

# 5. No raw SQL in Services
echo "Checking: Services must not contain raw SQL..."
SQL_IN_SERVICES=$(grep -rn '\->prepare(\|->query(' app/src/Services/ 2>/dev/null)
if [ -n "$SQL_IN_SERVICES" ]; then
    echo -e "${RED}❌ FAIL: Service contains raw SQL:${NC}"
    echo "$SQL_IN_SERVICES"
    ERRORS=$((ERRORS + 1))
else
    echo -e "${GREEN}✅ PASS${NC}"
fi

# ─────────────────────────────────────────────────────────────
# ENUM MAGIC STRING CHECKS
# ─────────────────────────────────────────────────────────────

echo ""
echo "── Enum Usage ──"

# 6. No magic strings for CmsItemType in Services/Controllers
echo "Checking: No magic strings for CmsItemType..."
CMS_ITEM_MAGIC=$(grep -rn "=== 'text'\|=== 'html'\|=== 'media'\|== 'TEXT'\|== 'HTML'\|== 'MEDIA'" \
    app/src/Services/ app/src/Controllers/ 2>/dev/null | grep -v "\.md:")
if [ -n "$CMS_ITEM_MAGIC" ]; then
    echo -e "${YELLOW}⚠️ WARNING: Magic strings found (use CmsItemType enum):${NC}"
    echo "$CMS_ITEM_MAGIC"
    WARNINGS=$((WARNINGS + 1))
else
    echo -e "${GREEN}✅ PASS${NC}"
fi

# 7. No magic strings for UserRoleName in Services/Controllers
echo "Checking: No magic strings for UserRoleName..."
ROLE_MAGIC=$(grep -rn "'Customer'\|'Employee'\|'Administrator'" \
    app/src/Services/ app/src/Controllers/ 2>/dev/null | grep -v "enum\|Enum\|getDisplayName")
if [ -n "$ROLE_MAGIC" ]; then
    echo -e "${YELLOW}⚠️ WARNING: Magic role strings found (use UserRoleName enum):${NC}"
    echo "$ROLE_MAGIC"
    WARNINGS=$((WARNINGS + 1))
else
    echo -e "${GREEN}✅ PASS${NC}"
fi

# 8. No hardcoded role/tier IDs (const *_ID patterns)
echo "Checking: No hardcoded ID constants for enums..."
HARDCODED_IDS=$(grep -rn "const.*_ROLE_ID\|const.*PRICE_TIER_\|const.*CUSTOMER.*= [0-9]" \
    app/src/Services/ 2>/dev/null)
if [ -n "$HARDCODED_IDS" ]; then
    echo -e "${YELLOW}⚠️ WARNING: Hardcoded enum IDs found (create backed enum):${NC}"
    echo "$HARDCODED_IDS"
    WARNINGS=$((WARNINGS + 1))
else
    echo -e "${GREEN}✅ PASS${NC}"
fi

# ─────────────────────────────────────────────────────────────
# EXCEPTION HANDLING CHECKS
# ─────────────────────────────────────────────────────────────

echo ""
echo "── Exception Handling ──"

# 9. Services should not catch generic \Exception (swallowing)
echo "Checking: Services should not swallow generic Exception..."
SWALLOWED=$(grep -rn "catch (\\\\Exception" app/src/Services/ 2>/dev/null)
if [ -n "$SWALLOWED" ]; then
    echo -e "${YELLOW}⚠️ WARNING: Service catches generic Exception (be specific):${NC}"
    echo "$SWALLOWED"
    WARNINGS=$((WARNINGS + 1))
else
    echo -e "${GREEN}✅ PASS${NC}"
fi

# 10. NotFoundException should exist and be used
echo "Checking: NotFoundException exists..."
if [ -f "app/src/Exceptions/NotFoundException.php" ]; then
    echo -e "${GREEN}✅ PASS (NotFoundException.php exists)${NC}"
else
    echo -e "${YELLOW}⚠️ WARNING: NotFoundException.php not found${NC}"
    WARNINGS=$((WARNINGS + 1))
fi

# ─────────────────────────────────────────────────────────────
# INFRASTRUCTURE CHECKS
# ─────────────────────────────────────────────────────────────

echo ""
echo "── Infrastructure ──"

# 11. Path resolution should be in Infrastructure, not Services
echo "Checking: Path resolution centralized in Infrastructure..."
PATH_IN_SERVICES=$(grep -rn "is_dir('/app/public')\|realpath(__DIR__.*public" app/src/Services/ 2>/dev/null)
if [ -n "$PATH_IN_SERVICES" ]; then
    echo -e "${YELLOW}⚠️ WARNING: Path resolution in Services (move to Infrastructure/PathResolver):${NC}"
    echo "$PATH_IN_SERVICES"
    WARNINGS=$((WARNINGS + 1))
else
    echo -e "${GREEN}✅ PASS${NC}"
fi

# 12. PathResolver should exist
echo "Checking: PathResolver exists..."
if [ -f "app/src/Infrastructure/PathResolver.php" ]; then
    echo -e "${GREEN}✅ PASS (PathResolver.php exists)${NC}"
else
    echo -e "${YELLOW}⚠️ WARNING: PathResolver.php not found${NC}"
    WARNINGS=$((WARNINGS + 1))
fi

# ─────────────────────────────────────────────────────────────
# VIEW MODEL USAGE IN VIEWS (sample check)
# ─────────────────────────────────────────────────────────────

echo ""
echo "── ViewModel Usage ──"

# 13. Views should access ViewModel properties, not raw array keys for viewModel variable
echo "Checking: Views use ViewModel properties (spot check)..."
RAW_ARRAY_ACCESS=$(grep -rn "\$viewModel\['[A-Z]" app/src/Views/ 2>/dev/null | head -3)
if [ -n "$RAW_ARRAY_ACCESS" ]; then
    echo -e "${YELLOW}⚠️ WARNING: View accesses ViewModel as array (should use ->property):${NC}"
    echo "$RAW_ARRAY_ACCESS"
    WARNINGS=$((WARNINGS + 1))
else
    echo -e "${GREEN}✅ PASS${NC}"
fi

# ─────────────────────────────────────────────────────────────
# CONTROLLER SIZE CHECK
# ─────────────────────────────────────────────────────────────

echo ""
echo "── Controller Size ──"

# 14. CmsDashboardController should be under 200 lines
echo "Checking: CmsDashboardController size..."
if [ -f "app/src/Controllers/CmsDashboardController.php" ]; then
    LINES=$(wc -l < "app/src/Controllers/CmsDashboardController.php")
    if [ "$LINES" -gt 200 ]; then
        echo -e "${YELLOW}⚠️ WARNING: CmsDashboardController has $LINES lines (target: <200)${NC}"
        WARNINGS=$((WARNINGS + 1))
    else
        echo -e "${GREEN}✅ PASS ($LINES lines)${NC}"
    fi
else
    echo "Skipped: File not found"
fi

# ─────────────────────────────────────────────────────────────
# SUMMARY
# ─────────────────────────────────────────────────────────────

echo ""
echo "═══════════════════════════════════════════════════════"
echo "                      SUMMARY"
echo "═══════════════════════════════════════════════════════"

if [ $ERRORS -eq 0 ] && [ $WARNINGS -eq 0 ]; then
    echo -e "${GREEN}✅ All checks passed${NC}"
    exit 0
elif [ $ERRORS -eq 0 ]; then
    echo -e "${YELLOW}⚠️ $WARNINGS warning(s) - review recommended${NC}"
    exit 0
else
    echo -e "${RED}❌ $ERRORS error(s), $WARNINGS warning(s)${NC}"
    echo "   Errors block merge. Warnings require review."
    exit 1
fi

