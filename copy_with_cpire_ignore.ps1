param(
    [string]$SourceDir = "",
    [string]$DestDir = "",
    [string]$CpIgnoreFile = ".cpignore"
)

# Set default values if not provided
if (-not $SourceDir) {
    $SourceDir = $PWD.Path
}

if (-not $DestDir) {
    $DestDir = Join-Path $PWD.Path "..\simplerest-pack"
}

# Function to read .cpignore patterns
function Get-CpIgnorePatterns {
    param([string]$CpIgnoreFile)
    
    if (Test-Path $CpIgnoreFile) {
        Get-Content $CpIgnoreFile | ForEach-Object {
            # Ignore empty lines and comments
            if ($_ -notmatch '^\s*(#|$)') {
                $_.Trim()
            }
        }
    } else {
        Write-Warning "The .cpignore file was not found in the source directory."
        return @()
    }
}

# Function to check if a file/directory should be excluded
function ShouldExcludeItem {
    param(
        [string]$ItemPath,
        [string]$SourceDir,
        [array]$Exclusions
    )
    
    $relativePath = Resolve-Path -Path $ItemPath -Relative -ErrorAction SilentlyContinue
    $relativePath = $relativePath.Substring(2) # Remove .\ prefix
    
    foreach ($pattern in $Exclusions) {
        # Handle directory patterns (ending with /)
        if ($pattern.EndsWith('/')) {
            $dirPattern = $pattern.TrimEnd('/')
            if ($relativePath -like "$dirPattern*" -or (Split-Path $relativePath -Parent) -like "$dirPattern*") {
                return $true
            }
        }
        # Handle file patterns with wildcards
        elseif ($pattern.Contains('*')) {
            if ($relativePath -like $pattern) {
                return $true
            }
        }
        # Handle exact matches
        else {
            if ($relativePath -eq $pattern -or (Split-Path $relativePath -Leaf) -eq $pattern) {
                return $true
            }
        }
    }
    
    return $false
}

# Function to copy directory recursively while respecting .cpignore
function Copy-WithCpIgnore {
    param(
        [string]$Source,
        [string]$Destination,
        [array]$Exclusions
    )
    
    # Create destination directory if it doesn't exist
    if (-not (Test-Path $Destination)) {
        New-Item -ItemType Directory -Path $Destination -Force | Out-Null
    }
    
    # Get all items in the source directory
    $items = Get-ChildItem -Path $Source -Recurse
    
    foreach ($item in $items) {
        $relativePath = Resolve-Path -Path $item.FullName -Relative -ErrorAction SilentlyContinue
        $relativePath = $relativePath.Substring(2) # Remove .\ prefix
        
        # Check if this item should be excluded
        if (ShouldExcludeItem -ItemPath $item.FullName -SourceDir $Source -Exclusions $Exclusions) {
            Write-Verbose "Excluding: $relativePath"
            continue
        }
        
        # Calculate destination path
        $destPath = Join-Path $Destination $relativePath
        
        if ($item.PSIsContainer) {
            # It's a directory
            if (-not (Test-Path $destPath)) {
                New-Item -ItemType Directory -Path $destPath -Force | Out-Null
            }
        } else {
            # It's a file
            $destDir = Split-Path $destPath -Parent
            
            if (-not (Test-Path $destDir)) {
                New-Item -ItemType Directory -Path $destDir -Force | Out-Null
            }
            
            Copy-Item -Path $item.FullName -Destination $destPath -Force
            Write-Verbose "Copied: $relativePath"
        }
    }
}

Write-Host "Copying SimpleRest from $SourceDir to $DestDir"
Write-Host "Using .cpignore file: $CpIgnoreFile"

# Get exclusion patterns
$exclusions = Get-CpIgnorePatterns $CpIgnoreFile

if ($exclusions.Count -gt 0) {
    Write-Host "Found $($exclusions.Count) exclusion patterns:"
    $exclusions | ForEach-Object { Write-Host "  - $_" }
} else {
    Write-Host "No exclusion patterns found, copying all files..."
}

# Perform the copy operation
Copy-WithCpIgnore -Source $SourceDir -Destination $DestDir -Exclusions $exclusions

Write-Host "Copy operation completed successfully!"
Write-Host "Destination: $DestDir"