#!/bin/bash

# Generate HTML documentation for GitHub Pages

mkdir -p docs

# Extract route information if available
if [ -f "app/Config/Routes.php" ]; then
    echo "Found Routes.php, extracting API endpoints..."
    # This will be used later in the HTML generation
fi

# Create main documentation page
cat > docs/index.html << 'EOF'
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CTA API Documentation</title>
    <style>
        body { 
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            max-width: 800px; 
            margin: 0 auto; 
            padding: 2rem;
            line-height: 1.6;
            background: #fff;
            color: #333;
        }
        .header { 
            border-bottom: 1px solid #eee; 
            padding-bottom: 1rem; 
            margin-bottom: 2rem; 
            text-align: center;
        }
        .endpoint { 
            background: #f8f9fa; 
            padding: 1rem; 
            margin: 1rem 0; 
            border-radius: 4px; 
            border-left: 4px solid #007bff;
        }
        .method { 
            display: inline-block; 
            padding: 0.2rem 0.5rem; 
            border-radius: 3px; 
            color: white; 
            font-weight: bold; 
            font-size: 0.8rem;
            margin-right: 0.5rem;
        }
        .get { background-color: #28a745; }
        .post { background-color: #007bff; }
        .put { background-color: #ffc107; color: #212529; }
        .delete { background-color: #dc3545; }
        code { 
            background: #f1f3f4; 
            padding: 0.2rem 0.4rem; 
            border-radius: 3px; 
            font-family: Monaco, Courier New, monospace;
        }
        pre { 
            background: #f8f9fa; 
            padding: 1rem; 
            border-radius: 4px; 
            overflow-x: auto;
            border: 1px solid #e9ecef;
        }
        h1 { color: #007bff; }
        h2 { 
            color: #495057; 
            border-bottom: 2px solid #e9ecef; 
            padding-bottom: 0.5rem; 
        }
        a { color: #007bff; text-decoration: none; }
        a:hover { text-decoration: underline; }
        .line-color { padding: 0.2rem 0.5rem; border-radius: 3px; color: white; font-weight: bold; }
        .red { background-color: #dc143c; }
        .blue { background-color: #0080ff; }
        .green { background-color: #228b22; }
        .brown { background-color: #8b4513; }
        .orange { background-color: #ff6600; }
        .purple { background-color: #800080; }
        .pink { background-color: #ff69b4; }
        .yellow { background-color: #ffd700; color: #333; }
    </style>
</head>
<body>
    <div class="header">
        <h1>🚊 CTA API Documentation</h1>
        <p>Chicago Transit Authority API built with CodeIgniter 4</p>
    </div>
    
    <h2>📖 Overview</h2>
    <p>This API provides access to Chicago Transit Authority (CTA) train data including station information, arrival times, and line details. Built using the CodeIgniter 4 framework.</p>
    
    <h2>🌐 Base URL</h2>
    <pre>https://your-domain.com/</pre>
    
    <h2>🛤️ API Endpoints</h2>
    
    <div class="endpoint">
        <h3><span class="method get">GET</span>/arrivals</h3>
        <p>Get train arrival information for stations</p>
        <h4>Parameters:</h4>
        <ul>
            <li><code>station_id</code> - Station identifier</li>
            <li><code>line</code> - Train line (optional)</li>
        </ul>
        <h4>Example:</h4>
        <pre><code>GET /arrivals?station_id=40380</code></pre>
    </div>
    
    <div class="endpoint">
        <h3><span class="method get">GET</span>/lines</h3>
        <p>Get information about available train lines</p>
        <h4>Example:</h4>
        <pre><code>GET /lines</code></pre>
    </div>
    
    <div class="endpoint">
        <h3><span class="method get">GET</span>/stations</h3>
        <p>Get list of all stations</p>
        <h4>Parameters:</h4>
        <ul>
            <li><code>line</code> - Filter by train line (optional)</li>
        </ul>
        <h4>Example:</h4>
        <pre><code>GET /stations?line=red</code></pre>
    </div>
    
    <h2>📋 Response Format</h2>
    <p>All responses are returned in JSON format.</p>
    
    <h3>✅ Success Response</h3>
    <pre><code>{
  "status": "success",
  "data": {
    // ... response data
  },
  "timestamp": "2023-08-23T10:30:00Z"
}</code></pre>
    
    <h3>❌ Error Response</h3>
    <pre><code>{
  "status": "error",
  "message": "Error description",
  "code": 400
}</code></pre>
    
    <h2>🚇 Train Lines</h2>
    <p>The following CTA train lines are available:</p>
    <ul>
        <li><span class="line-color red">Red Line</span></li>
        <li><span class="line-color blue">Blue Line</span></li>
        <li><span class="line-color green">Green Line</span></li>
        <li><span class="line-color brown">Brown Line</span></li>
        <li><span class="line-color orange">Orange Line</span></li>
        <li><span class="line-color purple">Purple Line</span></li>
        <li><span class="line-color pink">Pink Line</span></li>
        <li><span class="line-color yellow">Yellow Line</span></li>
    </ul>
    
    <h2>🚀 Getting Started</h2>
    <ol>
        <li>Clone the repository</li>
        <li>Run <code>composer install</code></li>
        <li>Copy <code>env</code> to <code>.env</code> and configure your settings</li>
        <li>Start the development server: <code>php spark serve</code></li>
        <li>Visit <code>http://localhost:8080</code></li>
    </ol>
    
    <h2>🔗 Links</h2>
    <ul>
        <li><a href="https://github.com/zkm/cta-api">📁 GitHub Repository</a></li>
        <li><a href="./openapi.json">📄 OpenAPI Specification (JSON)</a></li>
        <li><a href="https://editor.swagger.io/?url=https://zkm.github.io/cta-api/openapi.json">🔧 Interactive API Explorer</a></li>
        <li><a href="https://codeigniter4.github.io/CodeIgniter4/">📚 CodeIgniter 4 Documentation</a></li>
        <li><a href="https://www.transitchicago.com/developers/">🔌 CTA Developer Resources</a></li>
    </ul>
    
    <h2>📊 Project Stats</h2>
    <p>This project includes:</p>
    <ul>
        <li>Station data for all CTA lines</li>
        <li>Real-time arrival information</li>
        <li>RESTful API endpoints</li>
        <li>Built with modern PHP 8.1+</li>
        <li>Comprehensive test suite</li>
    </ul>
    
    <footer style="margin-top: 3rem; padding-top: 2rem; border-top: 1px solid #eee; text-align: center; color: #666;">
        <p>Generated automatically by GitHub Actions • Last updated: <span id="lastUpdate"></span></p>
        <script>
            document.getElementById('lastUpdate').textContent = new Date().toLocaleDateString();
        </script>
    </footer>
</body>
</html>
EOF

echo "Documentation generated successfully!"
