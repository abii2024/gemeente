#!/bin/bash

# Test script voor MCP Server
# Navigeer naar mcp-server directory
cd "$(dirname "$0")"

echo "🧪 Testing Gemeente Portal MCP Server..."
echo ""

# Check if dist exists
if [ ! -d "dist" ]; then
  echo "❌ dist/ folder not found. Running build..."
  npm run build
  echo ""
fi

# Test 1: Check if server starts
echo "📋 Testing if server starts..."
RESPONSE=$(echo '{"jsonrpc":"2.0","id":1,"method":"tools/list"}' | timeout 5 node dist/index.js 2>&1)

if echo "$RESPONSE" | grep -q "tools"; then
  echo "✅ Server is working!"

  # Count tools
  TOOL_COUNT=$(echo "$RESPONSE" | grep -o '"name"' | wc -l | tr -d ' ')
  echo "📊 Found $TOOL_COUNT tools available"

  echo ""
  echo "🎉 MCP Server is ready to use!"
else
  echo "❌ Server test failed"
  echo "Response: $RESPONSE"
fi

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""
echo "🎯 To use in VSCode:"
echo "   1. Press ⌘ + Shift + P"
echo "   2. Type: Developer: Reload Window"
echo "   3. Ask Copilot: 'Ga naar gemeente.test'"
echo ""
echo "� Example questions:"
echo "   • Ga naar de homepage en maak een screenshot"
echo "   • Log in op het dashboard"
echo "   • Haal alle klachten op"
echo "   • Dien een klacht in via het formulier"
echo ""
echo "🔧 Manual commands:"
echo "   npm start        - Start server"
echo "   npm run build    - Build TypeScript"
echo "   npm run dev      - Watch mode"
echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
