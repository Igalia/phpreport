import nextJest from 'next/jest.js'

const createJestConfig = nextJest({
  dir: './'
})

const config = {
  testEnvironment: 'jsdom',
  testPathIgnorePatterns: ['<rootDir>/.next/', '<rootDir>/node_modules/'],
  setupFilesAfterEnv: ['./jest.setup.ts'],
  moduleNameMapper: {
    '@/(.*)': '<rootDir>/src/$1'
  }
}

export default createJestConfig(config)
