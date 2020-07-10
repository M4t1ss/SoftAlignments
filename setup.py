import setuptools

with open("README.md", "r") as fh:
    long_description = fh.read()

setuptools.setup(
    name="softalignments",
    version="1.0.4",
    author="Matīss Rikters",
    author_email="m4t1ss@lielakeda.lv",
    description="Neural macine translation soft alignment visualisations for web and command line",
    long_description=long_description,
    long_description_content_type="text/markdown",
    url="https://github.com/M4t1ss/SoftAlignments",
    packages=setuptools.find_packages(),
    classifiers=[
        "Programming Language :: Python :: 3",
        "License :: OSI Approved :: MIT License",
        "Operating System :: OS Independent",
    ],
    python_requires='>=3.6',
    entry_points={
        'console_scripts': ['softalignments=softalignments.process_alignments:main'],
    },
)