#!/bin/bash
# View the API debug log

LOG_FILE=$(php -r "echo sys_get_temp_dir();")/recipe_api_debug.log

echo "======================================"
echo "Recipe API Debug Log"
echo "======================================"
echo "Log file: $LOG_FILE"
echo ""

if [ -f "$LOG_FILE" ]; then
    echo "Last 50 lines:"
    echo "--------------------------------------"
    tail -50 "$LOG_FILE"
    echo ""
    echo "======================================"
    echo "To watch live: tail -f $LOG_FILE"
    echo "To clear: rm $LOG_FILE"
else
    echo "❌ Log file not found. No API calls yet."
    echo ""
    echo "Try making a request first, then check again."
fi

